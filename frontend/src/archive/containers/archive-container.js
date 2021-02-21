import React, { Component } from "react";
import { connect } from "react-redux";

import { ArchiveRow } from "../components/archive-row";
import {loading} from "../../common/utils";
import {StencilArchiveList} from "../components/stencil-archive";
import {ArchiveList} from "../components/archive-list";
import {ArchiveError} from "../components/archive-error";
import {URLS} from "../../common/urls";

class ArchiveContainer extends Component {

  render() {

    const {
      content_failed,
      content_started,
      content_finished,
      empty,
      archive
    } = this.props;

    if (empty) {
      return (
        <div id="archive-empty" className="well">
          <p>Ingen filer eller linker!</p>
          <p>Det er visst ingen filer eller linker her. Du kan bidra ved å laste opp filer eller poste linker i panelet til høyre. Pass på at du leser våre <a href={URLS.terms}>retningslinjer</a> før du eventuelt gjør dette.</p>
          <p>- YouKok2</p>
        </div>
      );
    }

    if (content_failed) {
      return (
        <ArchiveList>
          <ArchiveError />
        </ArchiveList>
      );
    }

    const isLoading = loading(content_started, content_finished);

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
  content_started: archive.content_started,
  content_finished: archive.content_finished,
  content_failed: archive.content_failed,

  archive: archive.archive,
  empty: archive.empty,
});

export default connect(mapStateToProps, {})(ArchiveContainer);
