import { combineReducers } from 'redux';

import { archive } from './archive/reducer';

const rootReducer = combineReducers({
  archive
});

export default rootReducer;
