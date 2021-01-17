import React, { Component } from 'react';

import ArchiveContainer from '../archive-container';
import {Breadcrumbs} from "../../components/breadcrumbs";
import {connect} from "react-redux";
import {StencilArchiveTitle} from "../../components/stencil-archive";
import {ArchiveError} from "../../components/archive-error";
import {ArchiveList} from "../../components/archive-list";

class MainContainer extends Component {
  render() {

    const {
      data_finished,
      data_failed,

      requested_deletion,
      title,
      sub_title,
      parents,
    } = this.props;

    return (
      <React.Fragment>
        <div className="row">
          <div className="col-xs-12">
            <Breadcrumbs
              data_finished={data_finished}
              data_failed={data_failed}
              parents={parents}
            />
          </div>
        </div>
        {data_finished && requested_deletion &&
        <div className="row">
          <div className="col-xs-12 archive-title">
            <div className="alert alert-warning" role="alert">
              Bidrag knyttet til dette faget er fjernet etter forespørsel. Det kan derfor hende at innholdet du leter etter ikke lenger er tilgjengelig.
            </div>
          </div>
        </div>
        }
        <div className="row">
          <div className="col-xs-12 archive-title">
            <MainContainerTitle
              data_failed={data_failed}
              data_finished={data_finished}
              title={title}
              sub_title={sub_title}
            />
          </div>
        </div>
        <div className="row">
          <div className="col-xs-12">
            <MainContainerListing data_failed={data_failed}/>
          </div>
        </div>
      </React.Fragment>
    );
  };
}

const MainContainerTitle = ({ data_failed, data_finished, title, sub_title}) => {
  if (data_failed) {
    return (
      <h1>Noe gikk galt</h1>
    );
  }

  if (!data_finished) {
    return (
      <StencilArchiveTitle />
    );
  }

  return (
    <>
      <h1>{title}</h1>
      {sub_title && (
        <>
          &nbsp;
          <span>&ndash;</span>
          &nbsp;
          <h2>{sub_title}</h2>
        </>
      )}
    </>
  );
};

const MainContainerListing = ({ data_failed }) => {
  if (data_failed) {
    return (
      <div className="alert alert-warning" role="alert">
        <p>Mappen eller faget ble ikke funnet.</p>
      </div>
    );
  }

  return (
    <ArchiveContainer />
  );
}

const mapStateToProps = ({ archive }) => ({
  data_failed: archive.data_failed,
  data_finished: archive.data_finished,

  requested_deletion: archive.requested_deletion,
  title: archive.title,
  sub_title: archive.sub_title,
  parents: archive.parents,
});

export default connect(mapStateToProps, {})(MainContainer);
