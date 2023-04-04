<template>
  <img src="/pattern.svg" alt="pattern" class="fixed w-full h-full top-0 left-0 object-cover border opacity-10" />

  <div
    class="w-[64rem] max-w-[90vw] bg-white relative z-10 mx-auto mt-10 rounded-xl shadow-lg shadow-gray-100 flex overflow-hidden"
  >
    <div class="w-60 border-r overflow-y-auto custom-scroll">
      <div class="p-3">
        <div class="flex items-center rounded-full border overflow-hidden">
          <i class="uil uil-search pl-3"></i>
          <input
            v-model="users.search"
            type="text"
            class="outline-none flex-1 text-sm py-2 px-2"
            placeholder="Search user"
          />
        </div>
      </div>

      <list-chat
        v-for="user in users.data.filter((r) => r.name.toUpperCase().includes(users.search.toUpperCase()))"
        :key="user.id"
        :user-id="user.id"
        :user-name="user.name"
        :last-chat="user.email"
        :active="selectedUserId === user.id"
        @click="selectedUserId = user.id"
      />
    </div>

    <div class="flex-1 flex flex-col">
      <div class="border-b py-3 px-3 flex items-center">
        <div v-if="selectedUser" class="flex items-center gap-2">
          <img src="/user.png" alt="user" class="w-8" />
          <div>
            <span class="capitalize text-sm text-gray-700 block">{{ selectedUser.name }}</span>
            <span class="block text-xs text-gray-500">{{ selectedUser.email }}</span>
          </div>
        </div>

        <div class="flex-1"></div>

        <div>
          <button
            class="py-2 px-4 text-xs text-red-500 bg-red-50 hover:bg-red-100 focus:bg-red-100 rounded-lg"
            @click="logout"
          >
            Logout
          </button>
        </div>
      </div>

      <div ref="contentEl" class="h-[70vh] overflow-y-auto custom-scroll py-6 px-8">
        <div v-if="!selectedUserId" class="h-full flex justify-center items-center">
          <img src="/chatting.svg" alt="Chatting" class="w-3/12" />
        </div>

        <template v-else>
          <chat-item
            v-for="chat in chats.data"
            :key="chat.id"
            :sender="chat.from_id === authStore.user?.id"
            class="mb-3"
          >
            {{ chat.body }}
          </chat-item>
        </template>

        <div ref="topEl"></div>
      </div>

      <form v-if="selectedUserId" @submit.prevent="sendMessage" class="p-5">
        <div class="flex items-center rounded-full border overflow-hidden">
          <input v-model="message" type="text" class="outline-none flex-1 text-sm py-2 px-4" placeholder="Message" />

          <button type="submit">
            <i class="uil uil-message text-xl pr-3 text-gray-700"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
  import { computed, defineComponent, onMounted, reactive, ref, unref, watch } from 'vue';
  import { useRouter } from 'vue-router';
  import { io } from 'socket.io-client';

  import { UserInstance } from '@/types/user.type';
  import { ChatInstance } from '@/types/chat.type';

  import ListChat from '@/components/ListChat.vue';
  import ChatItem from '@/components/ChatItem.vue';

  import useAuthStore from '@/stores/auth.store';
  import useHandler from '@/composables/handler';

  export default defineComponent({
    components: {
      ListChat,
      ChatItem,
    },
    setup() {
      const router = useRouter();
      const authStore = useAuthStore();
      const { http, config } = useHandler();

      const socket = io(config.socketUrl, {
        query: {
          id: authStore.user?.id,
        },
      });

      const contentEl = ref<HTMLElement>();
      const topEl = ref<HTMLElement>();

      const users = reactive<{ loading: boolean; search: string; data: UserInstance[] }>({
        loading: false,
        search: '',
        data: [],
      });

      const chats = reactive<{ loading: boolean; data: ChatInstance[] }>({
        data: [],
        loading: false,
      });

      const selectedUserId = ref<number | null>(null);
      const message = ref('');

      const selectedUser = computed(() => users.data.find((r) => r.id === selectedUserId.value));

      const getRoom = async () => {
        users.loading = true;
        const req = await http.get('api/chats/rooms');
        users.loading = false;

        if (req?.status === 200) {
          users.data = req.data;
        }
      };

      socket.on('message', (chatId, message, toId, fromId) => {
        if (selectedUserId.value === parseInt(fromId)) {
          chats.data = [
            ...chats.data,
            {
              id: parseInt(chatId),
              to_id: parseInt(toId),
              from_id: parseInt(fromId),
              body: message,
            },
          ];
          scrollDown();
        }
      });

      const getMessage = async () => {
        chats.loading = true;
        chats.data = [];
        const req = await http.get(`api/chats/${selectedUserId.value}`);
        chats.loading = false;

        if (req?.status === 200) {
          chats.data = req.data;
          scrollDown();
        }
      };

      const sendMessage = async () => {
        const req = await http.post('api/chats', { to_id: selectedUserId.value, body: unref(message) });
        if (req?.status === 200) {
          chats.data = [...chats.data, { ...req.data }];
          socket.emit('message', req.data.id, req.data.body, req.data.to_id);
          message.value = '';
        }
      };

      const scrollDown = () => {
        setTimeout(() => {
          if (contentEl.value) {
            console.log(topEl.value?.offsetTop);
            contentEl.value.scroll({
              top: topEl.value?.offsetTop,
              behavior: 'smooth',
            });
          }
        }, 100);
      };

      const logout = async () => {
        await authStore.logout();
        socket.disconnect();
        router.push({ name: 'login' });
      };

      watch(selectedUserId, async (to) => {
        if (to) await getMessage();
      });

      onMounted(() => {
        getRoom();

        window.addEventListener('keydown', (e) => {
          if (e.keyCode === 27) {
            selectedUserId.value = null;
          }
        });
      });

      return {
        authStore,

        contentEl,
        topEl,

        users,
        selectedUserId,
        chats,
        selectedUser,
        message,

        sendMessage,
        logout,
      };
    },
  });
</script>
