import { combineReducers } from 'redux';

import form from './form/reducer';
import courses from "./courses/reducer";

const rootReducer = combineReducers({
  form,
  courses,
});

export default rootReducer;