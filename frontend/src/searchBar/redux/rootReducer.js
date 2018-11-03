import { combineReducers } from 'redux';

import form from './form/reducer';

const rootReducer = combineReducers({
  form,
});

export default rootReducer;