import { combineReducers } from 'redux';

import elements from './elements/reducer';

const rootReducer = combineReducers({
  elements
});

export default rootReducer;