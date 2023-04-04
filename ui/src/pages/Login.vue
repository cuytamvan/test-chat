<template>
  <div class="flex justify-center">
    <div class="max-w-full w-[800px] bg-white mt-20 flex gap-2 rounded-xl p-3 shadow-lg shadow-gray-200">
      <div class="flex-1 bg-purple-100 rounded-xl flex flex-col justify-center px-10">
        <img src="/vite.svg" alt="Logo" class="w-10" />
        <h1 class="text-gray-700 text-lg pt-3">
          Welcome to <span class="text-purple-500 font-bold">{{ $config.appName }}</span>
        </h1>
        <p class="text-gray-700 text-sm mt-2">
          Lorem ipsum, dolor sit amet consectetur adipisicing elit. Magnam, nobis?
        </p>
      </div>

      <div class="flex-1 py-10 px-5">
        <template v-if="data.validate">
          <h1 class="text-gray-700 text-lg pt-3">Verification</h1>
          <p class="text-sm text-gray-500">Pleace check your email</p>

          <form @submit.prevent="validate">
            <div class="grid gap-3 mt-5">
              <div>
                <label class="pb-2 block text-gray-700">OTP <span class="text-red-500">*</span></label>
                <input
                  v-model="data.otp"
                  type="text"
                  class="block py-3 px-5 w-full text-sm bg-gray-50 transition-all duration-200 outline-none rounded-xl border border-gray-100 focus:ring ring-purple-200"
                />
              </div>

              <div class="mt-2">
                <span v-if="message.error.length" class="text-red-400 mb-2 block">Error: {{ message.error }}</span>

                <button
                  class="bg-purple-100 focus:bg-purple-200 hover:bg-purple-200 text-purple-500 block w-full py-3 outline-none rounded-xl"
                >
                  Verify
                </button>
              </div>
            </div>
          </form>
        </template>

        <template v-else>
          <h1 class="text-gray-700 text-lg pt-3">Login</h1>
          <p class="text-sm text-gray-500">Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>

          <form @submit.prevent="login">
            <div class="grid gap-3 mt-10">
              <div>
                <label class="pb-2 block text-gray-700">Email <span class="text-red-500">*</span></label>
                <input
                  v-model="form.email"
                  type="text"
                  class="block py-3 px-5 w-full text-sm bg-gray-50 transition-all duration-200 outline-none rounded-xl border border-gray-100 focus:ring ring-purple-200"
                />
              </div>

              <div>
                <label class="pb-2 block text-gray-700">Password <span class="text-red-500">*</span></label>
                <input
                  v-model="form.password"
                  type="password"
                  class="block py-3 px-5 w-full text-sm bg-gray-50 transition-all duration-200 outline-none rounded-xl border border-gray-100 focus:ring ring-purple-200"
                />
              </div>

              <div class="mt-2">
                <span v-if="message.error.length" class="text-red-400 mb-2 block">Error: {{ message.error }}</span>
                <span v-if="message.success.length" class="text-green-400 mb-2 block">{{ message.error }}</span>

                <button
                  class="bg-purple-100 focus:bg-purple-200 hover:bg-purple-200 text-purple-500 block w-full py-3 outline-none rounded-xl"
                >
                  Login
                </button>
              </div>
            </div>
          </form>
        </template>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import { defineComponent, reactive, ref } from 'vue';
  import { useRouter } from 'vue-router';

  import useHandler from '@/composables/handler';
  import useAuthStore from '@/stores/auth.store';
  import { UserInstance } from '@/types/user.type';

  export default defineComponent({
    setup() {
      const router = useRouter();
      const { http } = useHandler();
      const authStore = useAuthStore();

      const form = ref({
        email: '',
        password: '',
      });

      const data = reactive({
        validate: false,
        otp: '',
      });

      const dataUser = ref<UserInstance | null>(null);
      const message = reactive({
        error: '',
        success: '',
      });

      const login = async () => {
        const req = await http.post('api/login', form.value);
        if (req?.status === 200) {
          dataUser.value = req.data;
          data.validate = true;
          message.success = req?.message || '';
        } else {
          message.error = req?.message || '';
        }
      };

      const validate = async () => {
        const req = await http.post('api/verify', {
          user_id: dataUser.value?.id,
          token: data.otp,
        });

        if (req?.status === 200) {
          localStorage.setItem('token', req.data.api_token);
          authStore.setUser(dataUser.value);
          router.push({ name: 'home' });
        } else {
          message.error = req?.message || '';
        }
      };

      return {
        form,
        data,
        message,

        login,
        validate,
      };
    },
  });
</script>
