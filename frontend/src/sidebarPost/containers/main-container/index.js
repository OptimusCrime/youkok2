import React from 'react';
import {TYPE_LINK, TYPE_UPLOAD} from "../../constants";
import LinkContainer from "../link-container";
import FileContainer from "../file-container";
import {connect} from "react-redux";
import {changeOpen as changeOpenDispatch} from "../../redux/main/actions";

const MainContainer = ({ open, changeOpen }) => (
  <div className="sidebar-element sidebar-create">
    <div className="sidebar-post sidebar-element-inner">
      <div className="sidebar-create__buttons">
        <button
          type="button"
          className="btn btn-default"
          onClick={() => changeOpen(TYPE_UPLOAD, open)}
        >
          Last opp fil
        </button>
        <button
          type="button"
          className="btn btn-default"
          onClick={() => changeOpen(TYPE_LINK, open)}
        >
          Post link
        </button>
      </div>
      <div className="sidebar-create-type">
        {open === TYPE_UPLOAD && <FileContainer />}
        {open === TYPE_LINK && <LinkContainer />}
        <div className="sidebar-create-terms">
          <p>Ved å poste linker eller laste opp filer godtar du våre <a href={window.SITE_DATA.archive_url_terms}>retningslinjer</a>.</p>
        </div>
      </div>
    </div>
  </div>
);

const mapStateToProps = ({ main }) => ({
  open: main.open
});

const mapDispatchToProps = {
  changeOpen: changeOpenDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(MainContainer);
