import { combineReducers } from 'redux';

import { boxes } from './boxes/reducer';
import {popularElements} from "./popular_elements/reducer";
import {popularCourses} from "./popular_courses/reducer";
import {lastDownloaded} from "./last_downloaded/reducer";
import {lastVisited} from "./last_visited/reducer";
import {newest} from "./newest/reducer";


const rootReducer = combineReducers({
  boxes,
  popularElements,
  popularCourses,
  newest,
  lastVisited,
  lastDownloaded,
});

export default rootReducer;
