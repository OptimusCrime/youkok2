import React from 'react';
import {connect} from "react-redux";

import {TYPE_LINK, TYPE_UPLOAD} from "../../constants";
import LinkContainer from "../link-container";
import FileContainer from "../file-container";
import {changeOpen as changeOpenDispatch} from "../../../archive/redux/main/actions";
import {URLS} from "../../../common/urls";

const MainContainer = ({ open, changeOpen, data_finished }) => (
  <div className="sidebar-element sidebar-create">
    <div className="sidebar-post sidebar-element-inner">
      <div className="sidebar-create__buttons">
        <button
          type="button"
          className={`btn btn-default${data_finished ? '' : ' disabled'}`}
          disabled={!data_finished}
          onClick={() => data_finished && changeOpen(TYPE_UPLOAD, open)}
        >
          Last opp fil
        </button>
        <button
          type="button"
          className={`btn btn-default${data_finished ? '' : ' disabled'}`}
          disabled={!data_finished}
          onClick={() => data_finished && changeOpen(TYPE_LINK, open)}
        >
          Post link
        </button>
      </div>
      <div className="sidebar-create-type">
        {open === TYPE_UPLOAD && <FileContainer />}
        {open === TYPE_LINK && <LinkContainer />}
        <div className="sidebar-create-terms">
          <p>Ved å poste linker eller laste opp filer godtar du våre <a href={URLS.terms}>retningslinjer</a>.</p>
        </div>
      </div>
    </div>
  </div>
);

const mapStateToProps = ({ main, archive }) => ({
  open: main.open,
  data_finished: archive.data_finished,
});

const mapDispatchToProps = {
  changeOpen: changeOpenDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);
