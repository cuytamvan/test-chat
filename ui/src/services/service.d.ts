import { ComponentCustomProperties } from 'vue';

import Config from './config';

declare module 'vue' {
  interface ComponentCustomProperties {
    $config: typeof Config;
  }
}
