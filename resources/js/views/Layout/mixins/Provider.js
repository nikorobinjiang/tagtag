export default {
  provide() {
    return {
      scatLayout: this,
      appName: this.appName,
      username: this.username,
    }
  },
};