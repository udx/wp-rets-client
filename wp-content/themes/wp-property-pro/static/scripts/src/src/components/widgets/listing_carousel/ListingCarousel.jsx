import React from 'react';
import Defaultlayout from './layouts/Defaultlayout.jsx';
import {Lib} from '../../../lib.jsx';

const ListingCarousel = ({widget_cell}) => {

  if (!widget_cell) {
    return null;
  }

  let container;
  switch (widget_cell.widget.fields.layout) {
    case 'default_layout':
    default:
      container = <Defaultlayout item={widget_cell.widget.fields}/>;
      break;
  }

  return (
    <section className={Lib.THEME_CLASSES_PREFIX+"listings"}>
      {container}
    </section>
  );
};

export default ListingCarousel;
