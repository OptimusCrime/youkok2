import React, { Component } from 'react';
import { connect } from 'react-redux';
import {TYPE_LINK, TYPE_NONE, TYPE_UPLOAD} from "../constants";

export class MainPostContainer extends Component {

  constructor(props) {
    super(props);

    this.state = {
      open: TYPE_NONE
    };

    this.changeType.bind = this.changeType;
  }

  changeType(control) {
    if (control === TYPE_NONE || this.state.open === control) {
      return this.setState({ open: TYPE_NONE });
    }

    this.setState({ open: control });
  }

  render() {
    return (
      <div className="sidebar-element">
        <div className="sidebar-post">
          <div className="sidebar-create-controlls">
            <button
              type="button"
              className="btn btn-default"
              onClick={() => this.changeType(TYPE_UPLOAD)}
            >
              Last opp fil
            </button>
            <button
              type="button"
              className="btn btn-default"
              onClick={() => this.changeType(TYPE_LINK)}
            >
              Post link
            </button>
          </div>
          <div className="sidebar-create-type">
            {this.state.open === TYPE_UPLOAD &&
              <p>Upload</p>
            }
            {this.state.open === TYPE_LINK &&
            <div className="sidebar-create-link">
              <div className="form-group">
                <label htmlFor="sidebar-create-link-url">URL</label>
                <input type="text" className="form-control" id="sidebar-create-link-url" placeholder="https://www.vg.no"/>
              </div>
              <div className="sidebar-create-submit">
                <button
                  type="button"
                  className="btn btn-default"
                >
                  Post link
                </button>&nbsp;
                eller <a href="#" onClick={e => {
                e.preventDefault();

                this.changeType(TYPE_NONE)
              }}>avbryt</a>.
              </div>
            </div>
            }
          </div>
          <div className="sidebar-create-terms">
            <p>Ved å poste linker eller laste opp filer godtar du våre <a href={SITE_DATA.archive_url_terms}>retningslinjer</a>.</p>
          </div>
        </div>
      </div>
    );
  }
}
