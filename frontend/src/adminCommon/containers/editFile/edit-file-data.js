import React from 'react';
import {Modal} from "../../components/modal";
import {ModalTitle} from "../../components/modal/modal-title";
import {ModalBody} from "../../components/modal/modal-body";
import {ModalFooter} from "../../components/modal/modal-footer";
import {hideEditFileModal, putAdminEditFile} from "../../redux/editFile/actions";
import {connect} from "react-redux";
import {TYPE_COURSE, TYPE_FILE, TYPE_LINK} from "../../../common/types";
import {
  ADMIN_EDIT_FILE_CHANGE_PARENT,
  ADMIN_EDIT_FILE_CHANGE_VALUE,
  ADMIN_EDIT_FILE_TOGGLE_CHECKBOX
} from "../../redux/editFile/constants";

class EditFileData extends React.Component {

  render() {

    const {
      fileId,
      courseId,
      data,
      putStarted
    } = this.props;

    console.log(data);

    const {
      title,
      id,
      parent,
      type,
      name,
      slug,
      uri,
      checksum,
      file_exists,
      size,
      course_tree,
      empty,
      directory,
      pending,
      deleted,
      requested_deletion,
      link
    } = data;

    return (
      <Modal
        onClose={this.props.hideEditFileModal}
      >
        <ModalTitle
          onClose={this.props.hideEditFileModal}
          title={title}
        />
        <ModalBody>
          <div className="form-group">
            <label htmlFor="element-name">
              ID
            </label>
            <input
              type="text"
              className="form-control"
              id="element-id"
              disabled={true}
              value={id || ''}
              onChange={() => {
              }}
            />
          </div>
          <div className="form-group">
            <label htmlFor="element-type">
              Type
            </label>
            <input
              type="text"
              className="form-control"
              id="element-type"
              disabled={true}
              value={type || ''}
              onChange={() => {
              }}
            />
          </div>
          <div className="form-group">
            <label htmlFor="element-name">
              Navn
            </label>
            <input
              type="text"
              className="form-control"
              id="element-name"
              value={name || ''}
              onChange={e => this.props.editFileChangeValue('name', e.target.value)}
            />
          </div>
          <div className="form-group">
            <label htmlFor="element-slug">
              Slug
            </label>
            <input
              type="text"
              className="form-control"
              id="element-slug"
              value={type === TYPE_LINK ? '' : (slug || '')}
              disabled={type === TYPE_LINK}
              onChange={e => this.props.editFileChangeValue('slug', e.target.value)}
            />
          </div>
          <div className="form-group">
            <label htmlFor="element-uri">
              URI
            </label>
            <input
              type="text"
              className="form-control"
              id="element-uri"
              value={type === TYPE_LINK ? '' : (uri || '')}
              disabled={type === TYPE_LINK}
              onChange={e => this.props.editFileChangeValue('uri', e.target.value)}
            />
          </div>
          {parent &&
          <div className="form-group">
            <label htmlFor="element-parent">
              Forelder
            </label>
            <select
              className="form-control"
              onChange={e => this.props.editFileChangeParent(e.target.value)}
              value={parent}
            >
              {course_tree.map(element =>
                <option
                  key={element.id}
                  value={element.id}
                >
                  {`${element.name}${element.id === id ? ' (Gjeldende element)' : ''}`}
                </option>
              )}
            </select>
          </div>
          }
          <div
            className={`form-group ${type !== TYPE_FILE ? '' : (file_exists ? 'has-success' : 'has-error')}`}
          >
            <label htmlFor="element-checksum">
              Checksum
            </label>
            <input
              type="text"
              className="form-control"
              id="element-checksum"
              value={type === TYPE_FILE ? (checksum || '') : ''}
              disabled={type !== TYPE_FILE}
              onChange={e => this.props.editFileChangeValue('checksum', e.target.value)}
            />
            {type === TYPE_FILE &&
            <span className="help-block">
              {file_exists ? 'File in place.' : 'File missing!'}
            </span>
            }
          </div>
          <div className="form-group">
            <label htmlFor="element-size">
              Størrelse
            </label>
            <input
              type="number"
              className="form-control"
              id="element-size"
              value={type === TYPE_FILE ? '' : (size || '')}
              disabled={type === TYPE_FILE}
              onChange={e => this.props.editFileChangeValue('size', e.target.value)}
            />
          </div>
          <div className="form-group">
            <label className="checkbox-inline">
              <input
                type="checkbox"
                checked={empty === 1}
                onChange={() => this.props.editFileToggleCheckbox('empty')}
              /> Empty
            </label>
            <label className="checkbox-inline">
              <input
                type="checkbox"
                checked={directory === 1}
                onChange={() => this.props.editFileToggleCheckbox('directory')}
              /> Directory
            </label>
            <label className="checkbox-inline">
              <input
                type="checkbox"
                checked={pending === 1}
                onChange={() => this.props.editFileToggleCheckbox('pending')}
              /> Pending
            </label>
            <label className="checkbox-inline">
              <input
                type="checkbox"
                checked={deleted === 1}
                onChange={() => this.props.editFileToggleCheckbox('deleted')}
              /> Deleted
            </label>
            {type === TYPE_COURSE &&
            <label className="checkbox-inline">
              <input
                type="checkbox"
                checked={requested_deletion === 1}
                onChange={() => this.props.editFileToggleCheckbox('requested_deletion')}
              /> Requested deletion
            </label>
            }
          </div>
          <div className="form-group">
            <label htmlFor="element-link">
              Link
            </label>
            <input
              type="url"
              className="form-control"
              id="element-link"
              value={link || ''}
              onChange={e => this.props.editFileChangeValue('link', e.target.value)}
            />
          </div>
        </ModalBody>
        <ModalFooter>
          <button
            type="button"
            className="btn btn-default"
            onClick={this.props.hideEditFileModal}
          >
            Lukk
          </button>
          <button
            type="button"
            className="btn btn-primary"
            disabled={putStarted}
            onClick={() => this.props.putAdminEditFile(fileId, courseId, data, false)}
          >
            {putStarted ? 'Vent litt...' : 'Lagre'}
          </button>
          <button
            type="button"
            className="btn btn-primary"
            disabled={putStarted}
            onClick={() => this.props.putAdminEditFile(fileId, courseId, data, true)}
          >
            {putStarted ? 'Vent litt...' : 'Lagre og lukk'}
          </button>
        </ModalFooter>
      </Modal>
    );
  }
}

const mapStateToProps = ({editFile}) => ({
  fileId: editFile.fileId,
  courseId: editFile.courseId,
  data: editFile.data,
  putStarted: editFile.putStarted,
});

const mapDispatchToProps = {
  hideEditFileModal,
  putAdminEditFile,
  editFileToggleCheckbox: id => ({type: ADMIN_EDIT_FILE_TOGGLE_CHECKBOX, id}),
  editFileChangeParent: parent => ({type: ADMIN_EDIT_FILE_CHANGE_PARENT, parent}),
  editFileChangeValue: (id, value) => ({type: ADMIN_EDIT_FILE_CHANGE_VALUE, id, value}),
};

export default connect(mapStateToProps, mapDispatchToProps)(EditFileData);
