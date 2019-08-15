import React, {Component} from 'react';
import {connect} from 'react-redux';
import {TYPE_COURSE, TYPE_DIRECTORY, TYPE_FILE, TYPE_LINK} from "../../../common/types";
import {showCreateDirectoryModal} from "../../redux/createDirectory/actions";
import {showEditFileModal} from "../../redux/editFile/actions";

const getFileIcon = type => {
  switch (type) {
    case TYPE_LINK:
      return "fa-link";
    case TYPE_FILE:
      return "fa-file-o";
    case TYPE_DIRECTORY:
    case TYPE_COURSE:
    default:
      return "fa-folder-o";
  }
};

class FileListingContainer extends Component {

  constructor(props) {
    super(props);

    // I know...
    this.state = props.files.reduce((obj, file) => ({
        ...obj,
        [`closed_${file.id}`]: false
      }),
      {}
    )
  }

  render() {

    const {
      files,
      course
    } = this.props;

    return files.map(file => (
        <ul
          className="file-listing"
          key={file.id}
        >
          <li>
            <div className="file-listing--inner">
              {file.children && file.children.length > 0 &&
              <i
                className={`fa file-listing--collapse fa-caret-${!this.state[`closed_${file.id}`] ? 'down' : 'up'}`}
                onClick={() => this.setState({[`closed_${file.id}`]: !this.state[`closed_${file.id}`]})}
              />
              }
              <i className={`fa file-listing--type ${getFileIcon(file.type)}`}/>
              <a
                href="#"
                onClick={e => {
                  e.preventDefault();
                  this.props.showEditFileModal(
                    course,
                    file.id
                  );
                }}
                className={`${file.deleted ? 'file-listing--deleted' : ''} ${file.pending ? 'file-listing--pending' : ''}`}
              >
                {file.type === TYPE_LINK ? `${file.name} (${file.link})` : file.name}
              </a>
              {(file.type === TYPE_COURSE || file.type === TYPE_DIRECTORY) &&
              <i
                className="fa fa-plus-square file-listing--subdir"
                onClick={() =>
                  this.props.showCreateDirectoryModal(
                    file.id,
                    course,
                    file.type === TYPE_COURSE ? `${type.courseCode}: ${file.courseName}` : file.name
                  )
                }
              />
              }
              <a
                href={file.url}
                target="_blank"
              >
                <i className="fa file-listing--external fa-external-link"/>
              </a>
            </div>
            {!this.state[`closed_${file.id}`] && file.children && file.children.length > 0 &&
            <div className="file-listing--children">
              <FileListingContainer
                files={file.children}
                course={course}
                showCreateDirectoryModal={this.props.showCreateDirectoryModal}
                showEditFileModal={this.props.showEditFileModal}
              />
            </div>
            }
          </li>
        </ul>
      )
    );
  }
}

const mapDispatchToProps = {
  showCreateDirectoryModal,
  showEditFileModal
};

export default connect(() => ({}), mapDispatchToProps)(FileListingContainer);
