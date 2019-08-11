import React from 'react';
import {Modal} from "../../components/modal";
import {ModalTitle} from "../../components/modal/modal-title";
import {ModalBody} from "../../components/modal/modal-body";
import {ModalFooter} from "../../components/modal/modal-footer";
import {connect} from "react-redux";
import {hideCreateDirectoryModal, postAdminCreateDirectory, updateModalValue} from "../../redux/createDirectory/actions";
import {KEYCODE_ENTER} from "../../../common/constants";

const CreateDirectoryModal = props => {

  const {
    title,
    value,
    directoryId,
    courseId
  } = props;

  return (
    <Modal>
      <ModalTitle
        onClose={props.hideCreateDirectoryModal}
        title={title}
      />
      <ModalBody>
        <div className="form-group">
          <label htmlFor="element-name">Navn</label>
          <input
            type="text"
            className="form-control"
            value={value}
            onChange={e =>  props.updateModalValue(e.target.value)}
            onKeyDown={e => {
              if (e.keyCode === KEYCODE_ENTER) {
                props.postAdminCreateDirectory(directoryId, courseId, value);
              }
            }}
          />
        </div>
      </ModalBody>
      <ModalFooter>
        <button
          type="button"
          className="btn btn-default"
          onClick={props.hideCreateDirectoryModal}
        >
          Lukk
        </button>
        <button
          type="button"
          className="btn btn-primary"
          onClick={() => props.postAdminCreateDirectory(directoryId, courseId, value)}
        >
          Lagre
        </button>
      </ModalFooter>
    </Modal>
  );
};

const mapStateToProps = ({createDirectory}) => ({
  title: createDirectory.title,
  value: createDirectory.value,
  directoryId: createDirectory.directoryId,
  courseId: createDirectory.courseId,
});

const mapDispatchToProps = {
  hideCreateDirectoryModal,
  updateModalValue,
  postAdminCreateDirectory
};

export default connect(mapStateToProps, mapDispatchToProps)(CreateDirectoryModal);
