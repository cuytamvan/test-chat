import { Api } from '@/services/api';
import config from '@/services/config';

const useHandler = () => {
  const http = Api();

  return {
    http,
    config,
  };
};

export default useHandler;
