import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

import { Api } from '@/services/api';

import { UserInstance } from '@/types/user.type';

const useAuthStore = defineStore('auth', () => {
  const http = Api();

  const user = ref<UserInstance | null>();
  const isLoggedin = computed(() => !!user.value);

  const setUser = (data: UserInstance | null) => {
    user.value = data;
  };

  const checkUser = async () => {
    const token = localStorage.getItem('token');
    if (!user.value && token) {
      const req = await http.get('api/me');
      if (req?.status === 200) {
        user.value = req.data;
      } else if (req?.status === 401) {
        localStorage.removeItem('token');
        user.value = null;
      }
    }
  };

  const logout = async () => {
    await http.post('api/logout');

    localStorage.removeItem('token');
    user.value = null;
  };

  return {
    user,

    isLoggedin,

    checkUser,
    logout,
    setUser,
  };
});

export default useAuthStore;
