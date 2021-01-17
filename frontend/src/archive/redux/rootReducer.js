import { combineReducers } from 'redux';

import { archive } from './archive/reducer';
import { history } from "./history/reducer";
import { file } from "./file/reducer";
import { link } from "./link/reducer";
import { main } from "./main/reducer";

const rootReducer = combineReducers({
  archive,
  history,
  file,
  link,
  main
});

export default rootReducer;
