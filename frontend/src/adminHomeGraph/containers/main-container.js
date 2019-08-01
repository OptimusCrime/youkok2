import React from 'react';
import {connect} from 'react-redux';
import ReactHighcharts from 'react-highcharts';

import {graph} from "../graph/graph-config";
import {loading} from "../../common/utils";

const AdminHomeGraph = ({started, finished, failed, data}) => {

  if (failed) {
    return (
      <p>Woops</p>
    );
  }

  const isLoading = loading(started, finished);

  if (isLoading) {
    return (
      <div className="admin-graph-loading">
        <em>Laster...</em>
      </div>
    );
  }

  const config = graph;
  config.series[0].data.push(...data);

  if (!isLoading) {
    return (
      <ReactHighcharts
        config={config}
      />
    );
  }
};

const mapStateToProps = ({graph}) => ({
  started: graph.started,
  finished: graph.finished,
  failed: graph.failed,
  data: graph.data,
});

export default connect(mapStateToProps, {})(AdminHomeGraph);
