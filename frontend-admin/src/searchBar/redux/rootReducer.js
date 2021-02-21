import { combineReducers } from 'redux';

import { form } from './form/reducer';
import { config } from './config/reducer';

const rootReducer = combineReducers({
  form,
  config,
});

export default rootReducer;
