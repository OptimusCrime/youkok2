import { combineReducers } from 'redux';

import { pending } from './pending/reducer';

const rootReducer = combineReducers({
  pending
});

export default rootReducer;
