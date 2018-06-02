import React from 'react'
import PropTypes from 'prop-types'
import Helmet from 'react-helmet'

import Header from '../components/Header'
import Footer from '../components/Footer'
import './index.scss'

const TemplateWrapper = ({ children }) => (
  <div>
    <Helmet
      title="Thought At Work 2018"
      meta={[
        { name: 'description', content: 'Sample' },
        { name: 'keywords', content: 'sample, something' },
		{ name: 'icon', type: 'image/ico', href: 'assets/graphics/favicon.png'},

      ]}
    />
<meta httpEquiv="x-ua-compatible"
				content="ie=edge"/>
			<meta name="format-detection" content="telephone=no"/>
	<meta httpEquiv="content-type"
				content="text/html; charset=UTF-8"/>
			<meta charSet="utf-8"/>
	<meta name="viewport"
				content="user-scalable=0, initial-scale=1.0, width=device-width, maximum-scale=1, minimum-scale=1" />
	<meta name="format-detection"
				content="telephone=no"/>
	<meta name="apple-mobile-web-app-capable"
				content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style"
				content="black"/>

			{/* open graph */}

			<meta property="og:url"                content="http://thoughtatwork.cias.rit.edu" />
			<meta property="og:type"               content="article" />
			<meta property="og:title"              content="Thought At Work, a student-run design conference" />
			<meta property="og:description"        content="Thought At Work is a three-day, student-run, student-focused design conference that takes place every October at Rochester Institute of Technology." />
			<meta property="og:image"              content="http://thoughtatwork.cias.rit.edu/assets/graphics/WebBanner_TAW2017.jpg" />
			<meta property="fb:app_id" 						 content="486507185043060"/>
			{/* Twitter Card data */}
			<meta name="twitter:card" content="product"/>
			<meta name="twitter:site" content="@TAW_RIT"/>
			<meta name="twitter:title" content="Thought At Work, a student-run design conference"/>
			<meta name="twitter:description" content="Thought At Work is a three-day, student-run, student-focused design conference that takes place every October at Rochester Institute of Technology."/>
			<meta name="twitter:creator" content="@TAW_RIT"/>
			<meta name="twitter:image" content="http://thoughtatwork.cias.rit.edu/assets/graphics/WebBanner_TAW2017.jpg"/>
			<meta name="description" content="Student-Run Design Conference"/>
			<meta name="title" content="Thought at Work"/>
			<link rel="icon" type="image/ico" href="assets/graphics/favicon.png"/>
			<base href="/"/>

    <Header />
    <div
      style={{
        margin: '0 auto',
        maxWidth: 960,
        padding: '0px 1.0875rem 1.45rem',
        paddingTop: 0,
      }}
    >
      {children()}
    </div>
    <Footer />
  </div>
)

TemplateWrapper.propTypes = {
  children: PropTypes.func,
}

export default TemplateWrapper
