import React, {Component} from 'react';
import {connect} from 'react-redux';
import {TYPE_COURSE, TYPE_DIRECTORY, TYPE_FILE, TYPE_LINK} from "../../common/types";

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

class FilesListingContainer extends Component {

  constructor(props) {
    super(props);

    // I know...
    this.state = props.files.reduce((obj, file) => ({
        ...obj,
        [`open_${file.id}`]: true
      }),
      {}
    )
  }

  render() {

    const {
      files
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
                className={`fa file-listing--collapse fa-caret-${this.state[`open_${file.id}`] ? 'down' : 'up'}`}
                onClick={() => this.setState({[`open_${file.id}`]: !this.state[`open_${file.id}`]})}
              />
              }
              <i className={`fa file-listing--type ${getFileIcon(file.type)}`}/>
              <a
                href="#"
                onClick={() => console.log('Click on file or folder or whater')}
                className={`${file.deleted ? 'file-listing--deleted' : ''} ${file.pending ? 'file-listing--pending' : ''}`}
                title={file.type === TYPE_LINK ? file.link : ''}
              >
                {file.name}
              </a>
              {(file.type === TYPE_COURSE || file.type === TYPE_DIRECTORY) &&
              <i className="fa fa-plus-square file-listing--subdir"/>
              }
              <a
                href={file.url}
                target="_blank"
                title={file.type === TYPE_LINK ? file.link : ''}
              >
                <i className="fa file-listing--external fa-external-link"/>
              </a>
            </div>
            {this.state[`open_${file.id}`] && file.children && file.children.length > 0 &&
            <div className="file-listing--children">
              <FilesListingContainer files={file.children}/>
            </div>
            }
          </li>
        </ul>
      )
    );
  }
}


export default connect(() => ({}), {})(FilesListingContainer);
