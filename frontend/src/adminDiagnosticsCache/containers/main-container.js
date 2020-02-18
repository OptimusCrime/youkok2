import React from 'react';
import {connect} from 'react-redux';

import {loading} from "../../common/utils";

const AdminDiagnosticsCache = ({started, finished, failed, data}) => {

  if (failed) {
    return (
      <p>Woops</p>
    );
  }

  const isLoading = loading(started, finished);

  if (isLoading) {
    return (
      <div className="admin-cache-loading">
        <em>Laster...</em>
      </div>
    );
  }

  if (!isLoading) {
    return (
      <ul className="list-group admin-cache-container">
        <div className="list-group-item admin-cache-row">
          <div className="admin-cache-label">
            <strong>
              {`Keys (${data.length})`}
            </strong>
          </div>
          <div className="admin-cache-value">
            <strong>Value</strong>
          </div>
        </div>
        {data.map(element => (
          <div className="list-group-item admin-cache-row" key={element.key}>
            <div className="admin-cache-label">
              <strong>{element.key}</strong>
            </div>
            <div className="admin-cache-value">
              {element.value}
            </div>
          </div>
        ))}
      </ul>
    );
  }
};

const mapStateToProps = ({graph}) => ({
  started: graph.started,
  finished: graph.finished,
  failed: graph.failed,
  data: graph.data,
});

export default connect(mapStateToProps)(AdminDiagnosticsCache);
