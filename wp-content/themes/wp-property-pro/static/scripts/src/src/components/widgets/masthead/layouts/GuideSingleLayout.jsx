import React from 'react';
import {Lib} from '../../../../lib.jsx';
import _ from 'lodash';

const GuideSingleLayout = ({widget_cell, headerStyle, returnToArchiveHandler, nextArticleHandler}) => {

  let prevLinkText = 'Return to Guide';
  let nextLinkText = 'Next Article';

  if (window.innerWidth < Lib.MOBILE_WIDTH) {
    prevLinkText = 'Guide';
    nextLinkText = 'Next';
  }

  return (
    <section className={Lib.THEME_CLASSES_PREFIX + "article-masthead"} style={headerStyle}>
      <header className={Lib.THEME_CLASSES_PREFIX + "article-header"}>
        {
          _.get(widget_cell, 'widget.fields.title', '')
            ? <h1 className={Lib.THEME_CLASSES_PREFIX + "guide-title"}>{widget_cell.widget.fields.title}</h1>
            : null
        }
        {
          _.get(widget_cell, 'widget.fields.subtitle', '')
            ? <p className={Lib.THEME_CLASSES_PREFIX + "article-excerpt"}>{widget_cell.widget.fields.subtitle}</p>
            : null
        }
      </header>
      <nav>
        <ol>
          <li className={Lib.THEME_CLASSES_PREFIX + "nav-item-prev"}>
            <a href="#" onClick={(eve) => {
              eve.preventDefault();
              returnToArchiveHandler();
            }}>
              <fa className="fa fa-arrow-left"></fa>
              {prevLinkText}
            </a>
          </li>
          <li className={Lib.THEME_CLASSES_PREFIX + "nav-item-next"}>
            <a href="#" onClick={(eve) => {
              eve.preventDefault();
              nextArticleHandler();
            }}>
              {nextLinkText}
              <fa className="fa fa-arrow-right"></fa>
            </a>
          </li>
        </ol>
      </nav>
    </section>
  );
};

export default GuideSingleLayout;