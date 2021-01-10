import { combineReducers } from 'redux';

import { main } from './main/reducer';
import { link } from './link/reducer';
import { file } from './file/reducer';

const rootReducer = combineReducers({
  main,
  link,
  file,
});

export default rootReducer;
