import {combineReducers} from 'redux';

import {createDirectory} from '../../common/redux/createDirectory/reducer';
import {files} from "../../common/redux/files/reducer";
import {editFile} from "../../common/redux/editFile/reducer";

const rootReducer = combineReducers({
  files,
  createDirectory,
  editFile
});

export default rootReducer;
