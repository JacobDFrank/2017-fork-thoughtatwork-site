import React, { Component } from 'react';
import classnames from 'classnames';
import Speaker from './../Speaker/Speaker.jsx';

export default class HomeSpeakers extends Component {
  constructor(props) {
    super(props);
    this.state = {
    };
  }

  render() {
    return (
      <div className={classnames('gridish-padding--top')}>
        <h1 className={classnames('container flex gridish-container gridish-container--complete gridish-grid__height--medium--13 gridish-grid__height--small--10 gridish-grid__height--xsmall--9')}>our lineup</h1>
        <Speaker/>
      </div>
    );
  }
}