const getters = {
  zoneStore: state => state.zone.length > 0 ? state.zone : '',
  zonePrefix: state => state.zone.length > 0 ? '/' + state.zone : '',
}
export default getters