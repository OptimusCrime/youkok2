import { combineReducers } from 'redux';

import frontpage from './frontpage/reducer';

const rootReducer = combineReducers({
  frontpage
});

export default rootReducer;