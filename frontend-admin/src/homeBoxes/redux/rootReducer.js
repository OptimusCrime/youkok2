import { combineReducers } from 'redux';

import { boxes } from './boxes/reducer';

const rootReducer = combineReducers({
  boxes
});

export default rootReducer;
