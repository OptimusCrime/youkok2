import { combineReducers } from 'redux';

import { boxes } from './boxes/reducer';
import {lastDownloaded} from "./last_downloaded/reducer";
import {lastVisited} from "./last_visited/reducer";
import {newest} from "./newest/reducer";

const rootReducer = combineReducers({
  boxes,
  lastDownloaded,
  lastVisited,
  newest,
});

export default rootReducer;
