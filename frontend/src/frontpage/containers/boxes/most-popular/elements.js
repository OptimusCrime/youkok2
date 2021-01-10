import React, { Component } from 'react';
import { connect } from 'react-redux';

import { BoxWrapper } from "../../../../common/components/box-wrapper";
import { ElementItem } from "../../../../common/components/element-item";
import { MostPopularDropdown } from "../../../components/most-popular/dropdown";
import {formatNumber, loading} from "../../../../common/utils";
import {
  DEFAULT_MOST_POPULAR_ELEMENTS_DELTA,
  DELTA_POST_POPULAR_ELEMENTS
} from "../../../consts";
import {EmptyItem} from "../../../../common/components/empty-item";
import {updateFrontpagePopularElements as updateFrontpagePopularElementsDispatch } from "../../../redux/popular_elements/actions";
import {getItem} from "../../../../common/local-storage";

class BoxMostPopularElements extends Component {

  constructor(props) {
    super(props);

    this.state = {
      open: false
    };

    this.toggleDropdown = this.toggleDropdown.bind(this);
    this.changeDelta = this.changeDelta.bind(this);
  }

  toggleDropdown() {
    this.setState({
      open: !this.state.open
    });
  }

  changeDelta(delta) {
    const { updateFrontpagePopularElements } = this.props;

    updateFrontpagePopularElements(delta);
  }

  render() {

    const {
      started,
      finished,
      failed,
      elements,
    } = this.props;

    const isLoading = loading(started, finished);

    if (failed) {
      return (
        <div className="col-xs-12 col-sm-6 frontpage-box">
          <BoxWrapper
            title="Mest populære"
            titleInline={false}
            isLoading={false}
            isEmpty={false}

          >
            <EmptyItem text="Kunne ikke hente mest populære" />
          </BoxWrapper>
        </div>
      );
    }

    const selected = getItem(DELTA_POST_POPULAR_ELEMENTS) || DEFAULT_MOST_POPULAR_ELEMENTS_DELTA;

    return (
      <div className="col-xs-12 col-sm-6 frontpage-box">
        <BoxWrapper
          title="Mest populære"
          titleInline={true}
          isLoading={isLoading}
          isEmpty={!isLoading && elements.length === 0}
          dropdown={
            <MostPopularDropdown
              selectedButton={selected}
              open={this.state.open}
              toggleDropdown={this.toggleDropdown}
              changeDelta={this.changeDelta}
            />
          }
        >
          {!isLoading && elements.map((element, index) =>
            <ElementItem element={element} key={index} additional={<span>[{formatNumber(element.downloads)}]</span>} /> )
          }
        </BoxWrapper>
      </div>
    );
  }
}

const mapStateToProps = ({ popularElements }) => ({
  started: popularElements.started,
  finished: popularElements.finished,
  failed: popularElements.failed,
  elements: popularElements.elements,
});

const mapDispatchToProps = {
  updateFrontpagePopularElements: updateFrontpagePopularElementsDispatch
};

export default connect(mapStateToProps, mapDispatchToProps)(BoxMostPopularElements);
