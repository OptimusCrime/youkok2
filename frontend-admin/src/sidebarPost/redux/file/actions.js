import {
  SIDEBAR_POST_FILE_ADD_FILE,

  SIDEBAR_POST_FILE_UPLOADS_FINISHED,
  SIDEBAR_POST_FILE_UPLOADS_STARTED,

  SIDEBAR_POST_FILE_UPLOAD_STARTED,
  SIDEBAR_POST_FILE_UPLOAD_FINISHED,
  SIDEBAR_POST_FILE_UPLOAD_FAILED,
} from "./constants";
import {uploadFileRest} from "../../api";
import {isValidFile} from "../../utilities";

export const addFiles = files => dispatch => {
  files.forEach(file => {
    if (!isValidFile(file)) {
      alert(`${file.name} er ikke av godkjent type.`);
    }
    else {
      dispatch({ type: SIDEBAR_POST_FILE_ADD_FILE, file: file});
    }
  });
};

export const uploadFiles = files => dispatch => {
  dispatch({ type: SIDEBAR_POST_FILE_UPLOADS_STARTED });

  const promises = [];

  files.forEach((file, index) => {
    promises.push(uploadFile(file, index, dispatch))
  });

  Promise.all(promises).then(() => {
    dispatch({ type: SIDEBAR_POST_FILE_UPLOADS_FINISHED });
  });
};

const uploadFile = (file, index, dispatch) => {
  dispatch({ type: SIDEBAR_POST_FILE_UPLOAD_STARTED, index });

  return uploadFileRest(file.data)
    .then(response => response.json())
    .then(() => dispatch({ type: SIDEBAR_POST_FILE_UPLOAD_FINISHED, index }))
    .catch(() => dispatch({type: SIDEBAR_POST_FILE_UPLOAD_FAILED, index }));
};
