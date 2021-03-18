import {
  isArray,
  isObject,
  cloneDeep
} from 'lodash';

let genFormSetMutationsActions = (storeModule) => {
  let mutations = {};
  let actions = {};
  ['data', 'form'].forEach(field => {
    if (storeModule.state[field]) {
      Object.keys(storeModule.state[field]).forEach((key) => {
        let baseKey = [field, key].join('.');
        // default
        mutations[baseKey.toUpperCase()] = (state, message) => {
          state[field][key] = message;
        }
        actions[baseKey] = ({
          commit
        }, message) => {
          commit(baseKey.toUpperCase(), message)
        }

        // assign
        mutations[baseKey.toUpperCase() + '@assign'] = (state, message) => {
          if (isArray(message) || isObject(message)) {
            let load = {};
            load[key] = Object.assign(state[field][key], message);
            state[field] = Object.assign(state[field], load);
          } else {
            state[field][key] = message;
          }
        }
        actions[baseKey + '@assign'] = ({
          commit
        }, message) => {
          commit(baseKey.toUpperCase() + '@assign', message)
        }

        // replace
        mutations[baseKey.toUpperCase() + '@replace'] = (state, message) => {
          state[field][key] = message;
        }
        actions[baseKey + '@replace'] = ({
          commit
        }, message) => {
          commit(baseKey.toUpperCase() + '@replace', message)
        }
      });
      storeModule.mutations = Object.assign(storeModule.mutations, mutations);
      storeModule.actions = Object.assign(storeModule.actions, actions);
    }
  });
}

let genExtra = (storeModule, formInit) => {
  ['assign_form'].forEach((key) => {
    storeModule.mutations[key.toUpperCase()] = (state, message) => {
      state.form = cloneDeep(Object.assign({}, state.form, message));
    }
    storeModule.actions[key] = ({
      commit
    }, message) => {
      commit(key.toUpperCase(), message)
    }
  });
  ['clear'].forEach((key) => {
    storeModule.mutations[key.toUpperCase()] = (state) => {
      if (formInit) {
        state.form = cloneDeep(formInit);
      }
    }
    storeModule.actions[key] = ({
      commit
    }) => {
      commit(key.toUpperCase())
    }
  });
}

export const storeModuleExtra = (storeModule, formInit) => {
  genFormSetMutationsActions(storeModule);
  genExtra(storeModule, formInit);

  return storeModule;
}

export const mapStateGetSet = normalizeNamespace((namespace, states) => {
  const res = {};
  normalizeMap(states).forEach(({
    key,
    val
  }) => {
    res[key] = {};
    if (val.get) {
      res[key].get = function mappedStateGet() {
        let state = this.$store.state
        let getters = this.$store.getters
        if (namespace) {
          const module = getModuleByNamespace(this.$store, 'mapState', namespace)
          if (!module) {
            return
          }
          state = module.context.state
          getters = module.context.getters
        }
        return typeof val.get === 'function' ?
          val.get.call(this, state, getters) :
          state[val.get]
      }
    }
    if (val.set) {
      res[key].set = function mappedStateSet(payload) {
        let dispatch = this.$store.dispatch
        if (namespace) {
          const module = getModuleByNamespace(this.$store, 'mapState', namespace)
          if (!module) {
            return
          }
          dispatch = module.context.dispatch
        }
        typeof val.set === 'function' ?
          val.set.call(this, payload, dispatch) :
          dispatch(val.set, payload);
      }
    }
    // mark vuex getter for devtools
    res[key].vuex = true
  })
  return res
});

function normalizeMap(map) {
  return Array.isArray(map) ?
    map.map(key => ({
      key,
      val: key
    })) :
    Object.keys(map).map(key => ({
      key,
      val: map[key]
    }))
}

function normalizeNamespace(fn) {
  return (namespace, map) => {
    if (typeof namespace !== 'string') {
      map = namespace
      namespace = ''
    } else if (namespace.charAt(namespace.length - 1) !== '/') {
      namespace += '/'
    }
    return fn(namespace, map)
  }
}

function getModuleByNamespace(store, helper, namespace) {
  const module = store._modulesNamespaceMap[namespace]
  if (process.env.NODE_ENV !== 'production' && !module) {
    console.error(`[vuex] module namespace not found in ${helper}(): ${namespace}`)
  }
  return module
}