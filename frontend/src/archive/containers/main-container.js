import React, { Component } from 'react';
import { connect } from 'react-redux';

import ArchiveContainer from './archive-container';

import { fetchArchive } from '../redux/archive/actions';
import {loading} from "../../common/utils";

class MainContainer extends Component {

  componentDidMount() {
    this.props.fetchArchive();
  }

  render() {

    const {
      failed
    } = this.props;

    if (failed) {
      return (
        <p>Vi har visst litt tekniske problemer her...</p>
      );
    }

    return (
      <React.Fragment>
        <div className="row">
          <div className="col-xs-12" id="archive-title">
            <h1>Title</h1>
            <span>&mdash;</span>
            <h2>Subtitle</h2>
          </div>
        </div>
        <div className="row">
          <div className="col-xs-12">
            <ArchiveContainer />
          </div>
        </div>
      </React.Fragment>
    );
  }
}

const mapStateToProps = ({ archive }) => ({
  failed: archive.failed,
});

const mapDispatchToProps = {
  fetchArchive
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);