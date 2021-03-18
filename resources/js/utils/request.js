import axios from 'axios'
import {
  Message
} from 'element-ui'
import store from '@/js/store'
//import { getToken } from '@/js/elementAdmin/utils/auth'


// 基础组件
const service = () => {
  // create an axios instance
  const service = axios.create({
    baseURL: process.env.BASE_API, // api的base_url
    timeout: 60000 // request timeout
  });

  service.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

  // request interceptor
  service.interceptors.request.use(config => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
      config.headers.common['X-CSRF-TOKEN'] = token.content;
    } else {
      console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
    }
    return config
  }, error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  })

  // respone interceptor
  service.interceptors.response.use(
    /**
     * 下面的注释为通过在response里，自定义code来标示请求状态
     * 当code返回如下情况则说明权限有问题，登出并返回到登录页
     * 如想通过xmlhttprequest来状态码标识 逻辑可写在下面error中
     * 以下代码均为样例，请结合自生需求加以修改，若不需要，则可删除
     */
    response => {
      const res = response.data
      if (
        (res.code && res.code !== 200 ||
          res.result && res.result !== 'success') &&
        !res.should_confirm
      ) {
        Message({
          message: res.message,
          type: 'error',
          duration: 5 * 1000
        })
        // // 50008:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
        // if (res.code === 50008 || res.code === 50012 || res.code === 50014) {
        //   // 请自行在引入 MessageBox
        //   // import { Message, MessageBox } from 'element-ui'
        //   MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
        //     confirmButtonText: '重新登录',
        //     cancelButtonText: '取消',
        //     type: 'warning'
        //   }).then(() => {
        //     store.dispatch('FedLogOut').then(() => {
        //       location.reload() // 为了重新实例化vue-router对象 避免bug
        //     })
        //   })
        // }
        return Promise.reject(res.errors)
      } else {
        return response
      }
    },
    error => {
      Message({
        message: error.response.data.message || error.message,
        type: 'error',
        duration: 5 * 1000
      })
      return Promise.reject(error)
    }
  );

  return service;
}

// 重试机制
const addRetry = (service) => {
  service.defaults.retry = 1; //重试次数
  service.defaults.retryDelay = 1000; //重试延时
  service.defaults.shouldRetry = (error) => true; //重试条件，默认只要是错误都需要重试
  service.interceptors.response.use(undefined, (err) => {
    var config = err.config ? err.config : null;
    // 判断是否配置了重试
    if (!config || !config.retry) return Promise.reject(err);

    if (!config.shouldRetry || typeof config.shouldRetry != 'function') {
      return Promise.reject(err);
    }

    //判断是否满足重试条件
    if (!config.shouldRetry(err)) {
      return Promise.reject(err);
    }

    // 设置重置次数，默认为0
    config.__retryCount = config.__retryCount || 0;

    // 判断是否超过了重试次数
    if (config.__retryCount >= config.retry) {
      return Promise.reject(err);
    }

    //重试次数自增
    config.__retryCount += 1;

    //延时处理
    var backoff = new Promise(function (resolve) {
      setTimeout(function () {
        resolve();
      }, config.retryDelay || 1);
    });

    //重新发起axios请求
    return backoff.then(function () {
      return axios(config);
    });
  });

  return service;
}

// 延迟机制
let _requestDelayCount = 0;
const addDelay = (service, delay = 100) => {
  return (args, ms) => {
    ms = ms ? ms : delay;
    return new Promise((resolve, reject) => {
      setTimeout(_ => {
        _requestDelayCount--;
        return resolve(service(args));
      }, ms * _requestDelayCount++);
    });
  }
}

// 生成器
const generator = (args, config) => {
  let ins = service();
  if (config) {
    if (config.retry) {
      ins = addRetry(ins);
    }
    if (config.delay && !isNaN(config.delay)) {
      ins = addDelay(ins, config.delay);
    }
  }
  return ins(args);
}

// 默认服务 (args)
export default generator;

// 延迟请求：(args, ms)
export const requestDelay = addDelay(service());

// 重试请求：(args)
export const requestRetry = addRetry(service());

// 获取默认请求头
export const getHeaders = function () {
  let headers = {
    'X-Requested-With': 'XMLHttpRequest',
  };
  const token = document.head.querySelector('meta[name="csrf-token"]');
  if (token) {
    headers['X-CSRF-TOKEN'] = token.content;
  }
  return headers;
};