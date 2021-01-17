import React from 'react';
import {connect} from "react-redux";
import Dropzone from 'react-dropzone'

import {
  TYPE_NONE,
  TYPE_UPLOAD
} from "../../constants";
import {changeOpen as changeOpenDispatch} from "../../../archive/redux/main/actions";
import {
  addFiles as addFilesDispatch,
  uploadFiles as uploadFilesDispatch,
} from "../../../archive/redux/file/actions";
import {
  SIDEBAR_POST_FILE_REMOVE_FILE,
  SIDEBAR_POST_FILE_RESET
} from "../../../archive/redux/file/constants";
import {calculateProgress, humanReadableFileSize} from "../../utilities";
import {FileUploadMessage} from "../../components/upload-message";

const FileContainer = props => {

  const {
    reset,
    changeOpen,
    addFiles,
    removeFile,
    uploadFiles,

    files,
    upload_started,
    upload_finished,

    course_id,
    valid_file_types,
    max_file_size_bytes,
  } = props;

  const progress = Math.floor(calculateProgress(files));

  return (
    <div className="sidebar-create__inner sidebar-create__file">
      <strong>Velg filer</strong>
      <div className="sidebar-create__file--wrapper">
        <div className="sidebar-create__file--list">
          {files.map((file, index) => (
            <div className="sidebar-create__file--element" key={index}>
              <div className="sidebar-create__file--element--meta">
                <div className="sidebar-create__file--element--name">
                  {file.data.name}
                </div>
                <div className="sidebar-create__file--element--size">
                  {humanReadableFileSize(file.data.size)}
                </div>
              </div>
              {!(upload_started || upload_finished) &&
              <div className="sidebar-create__file--dropzone-file__remove">
                <a href="#" onClick={e => {
                  e.preventDefault();

                  removeFile(index);
                }}>
                  Fjern
                  &nbsp;
                  <i className="fa fa-times"/>
                </a>
              </div>
              }
              {file.failed &&
              <div className="sidebar-create__file--dropzone-file__fail">
                <em>Kunne ikke laste opp fil</em>
              </div>
              }
            </div>
          ))}
        </div>
        <div className="sidebar-create__file--dropzone">
          <Dropzone onDrop={file => addFiles(valid_file_types, max_file_size_bytes, file)}>
            {({getRootProps, getInputProps}) => (
              <div className="sidebar-create__file--dropzone--inner" {...getRootProps()}>
                <div className="sidebar-create__file--dropzone--button">
                  <input
                    {...getInputProps()}
                    disabled={upload_started || upload_finished}
                  />
                  <span
                    className={`btn btn-default ${(upload_started || upload_finished) ? 'disabled' : ''}`}
                  >
                    Legg til filer
                  </span>
                </div>
              </div>
            )}
          </Dropzone>
        </div>
        {(upload_started || upload_finished) &&
        <div className="sidebar-create__file--progress">
          <div
            className="sidebar-create__file--progress--inner"
            style={{
              width: `${progress}%`
            }}
          />
          <span>
            {`${progress}%`}
          </span>
        </div>
        }
      </div>
      <div className="sidebar-create__file--valid">
        <p>{`Godkjente  filtyper: ${valid_file_types.join(', ')}.`}</p>
      </div>
      <div className="sidebar-create-submit">
        <button
          type="button"
          className="btn btn-default"
          onClick={() => {
            if (upload_finished) {
              reset();
            } else {
              if (!upload_started && files.length > 0) {
                uploadFiles(course_id, files);
              }
            }
          }}
          disabled={!upload_finished && (upload_started || files.length === 0)}
        >
          {upload_finished ? 'Last opp flere filer' : (upload_started ? 'Vent litt' : 'Last opp')}
        </button>
        &nbsp;
        eller
        &nbsp;
        <a href="#" onClick={e => {
          e.preventDefault();

          reset();
          changeOpen(TYPE_NONE, TYPE_UPLOAD);
        }}>
          {upload_finished
            ? 'gå tilbake'
            : 'avbryt'
          }
        </a>.
      </div>
      {upload_finished && <FileUploadMessage files={files}/>}
    </div>
  );
};

const mapStateToProps = ({file, archive}) => ({
  files: file.files,
  upload_started: file.upload_started,
  upload_finished: file.upload_finished,
  course_id: archive.course_id,
  valid_file_types: archive.valid_file_types,
  max_file_size_bytes: archive.max_file_size_bytes,
});

const mapDispatchToProps = {
  changeOpen: changeOpenDispatch,
  addFiles: addFilesDispatch,
  uploadFiles: uploadFilesDispatch,
  reset: () => ({type: SIDEBAR_POST_FILE_RESET}),
  removeFile: index => ({type: SIDEBAR_POST_FILE_REMOVE_FILE, index})
};

export default connect(mapStateToProps, mapDispatchToProps)(FileContainer);
