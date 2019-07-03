import React, { Component } from "react";
import { connect } from "react-redux";

import { ArchiveRow } from "../components/archive-row";
import {loading} from "../../common/utils";
import {StencilArchiveList} from "../components/stencil-archive-list";
import {ArchiveList} from "../components/archive-list";
import {ArchiveError} from "../components/archive-error";

class ArchiveContainer extends Component {

  render() {

    const {
      failed,
      started,
      finished,
      archive
    } = this.props;

    if (window.SITE_DATA.archive_empty) {
      return (
        <div id="archive-empty" className="well">
          <p>Ingen filer eller linker!</p>
          <p>Det er visst ingen filer eller linker her. Du kan bidra ved å laste opp filer eller poste linker i panelet til høyre. Pass på at du leser våre <a href="#">retningslinjer</a> før du eventuelt gjør dette.</p>
          <p>- YouKok2</p>
        </div>
      );
    }

    if (failed) {
      return (
        <ArchiveList>
          <ArchiveError />
        </ArchiveList>
      );
    }

    const isLoading = loading(started, finished);

    if (isLoading) {
      return (
        <ArchiveList>
          <StencilArchiveList size={10} />
        </ArchiveList>
      );
    }

    return (
      <ArchiveList>
        {archive.map((item, key) => <ArchiveRow key={key} item={item} /> )}
      </ArchiveList>
    );
  }
}

const mapStateToProps = ({ archive }) => ({
  failed: archive.failed,
  started: archive.started,
  finished: archive.finished,
  archive: archive.archive
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(ArchiveContainer);
