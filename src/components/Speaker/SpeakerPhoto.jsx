import React from 'react';
import classnames from 'classnames';
import styles from '../../styles/components/speakers/speakerPhoto.module.scss';

let SpeakerPhoto = function statelessFunctionComponentClass(props) {
  // let imageURL = './assets/speaker-headshots/' + {props.firstName + '_' + props.lastName + '.jpg'};
  let imageURL = './assets/speaker-headshots/' + 'arnaud_tanielian.jpg';


  return (
    <div>
      <img className={classnames(styles.headshotContainer)}
        src={__PATH_PREFIX__ + imageURL}
      />
      {/* <img className={classnames(styles.headshotContainer)}
        src={arno}
      /> */}
      <h2 className={classnames(styles.speakerCard_text__spacing)}>
        {props.firstName + ' ' + props.lastName}
      </h2>
      <p>{props.position}</p>
    </div>
  );
};

export default SpeakerPhoto;
