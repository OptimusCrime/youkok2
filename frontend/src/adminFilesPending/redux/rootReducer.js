import {combineReducers} from 'redux';

import {createDirectory} from '../../adminCommon/redux/createDirectory/reducer';
import {files} from "../../adminCommon/redux/files/reducer";
import {editFile} from "../../adminCommon/redux/editFile/reducer";

const rootReducer = combineReducers({
  files,
  createDirectory,
  editFile
});

export default rootReducer;
