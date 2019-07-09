import { combineReducers } from 'redux';

import { main } from './main/reducer';
import { link } from './link/reducer';

const rootReducer = combineReducers({
  main,
  link,
});

export default rootReducer;
