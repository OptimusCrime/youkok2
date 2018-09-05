import React, { Component } from "react";
import { connect } from "react-redux";

import { ArchiveRow } from "../components/archive-row";
import {loading} from "../../common/utils";
import {StencilArchiveList} from "../components/stencil/archive-list";

class ArchiveContainer extends Component {

  render() {

    const {
      started,
      finished,
      archive
    } = this.props;

    const isLoading = loading(started, finished);

    if (isLoading || (archive.content && archive.content.length > 0)) {
      return (
        <div className="archive-list">
          <div className="archive-row archive-row--header">
            <div className="archive-row-icon">
            </div>
            <div className="archive-row-name">
              <strong>Navn</strong>
            </div>
            <div className="archive-row-downloads">
              <strong>Nedlastninger</strong>
            </div>
            <div className="archive-row-age">
              <strong>Postet</strong>
            </div>
          </div>
          {isLoading && <StencilArchiveList size={10} />}
          {!isLoading && archive.content.map((item, key) => <ArchiveRow key={key} item={item} /> )}
        </div>
      );
    }

    return (
      <div id="archive-empty" className="well">
        <p>Ingen filer eller linker!</p>
        <p>Det er visst ingen filer eller linker her. Du kan bidra ved å laste opp filer eller poste linker i panelet til høyre. Pass på at du leser våre <a href="#">retningslinjer</a> før du eventuelt gjør dette.</p>
        <p>- YouKok2</p>
      </div>
    );
  }
}

const mapStateToProps = ({ archive }) => ({
  started: archive.started,
  finished: archive.finished,
  archive: archive.archive
});

const mapDispatchToProps = {
};

export default connect(mapStateToProps, mapDispatchToProps)(ArchiveContainer);