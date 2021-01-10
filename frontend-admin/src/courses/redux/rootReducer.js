import { combineReducers } from 'redux';

import { courses } from './courses/reducer';

const rootReducer = combineReducers({
  courses
});

export default rootReducer;
