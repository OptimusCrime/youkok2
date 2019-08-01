import { combineReducers } from 'redux';

import { graph } from './graph/reducer';

const rootReducer = combineReducers({
  graph
});

export default rootReducer;
