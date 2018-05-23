import React from "react";
import Sponsor from "./Sponsor.jsx";
import FooterBottom from "./FooterBottom.jsx";
import BeSponsor from "./BeSponsor.jsx";
import data from "../../assets/sponsors.json";
import ScrollToTop from 'react-scroll-up';

class Footer extends React.Component {
    createSponsor = sponsor => {
        return (
            <Sponsor
                imageURL={sponsor.image}
                key={sponsor.name}
                imageName={sponsor.name}
                imageSite={sponsor.site}
            />
        );
    };

    createSponsors = sponsors => {
        return sponsors.map(this.createSponsor);
    };

    render() {
        return (
            <div className="footer">
				<ScrollToTop showUnder={160}>
					<div id="return-to-top" className="toTop">
					    <hr className="toTop-hr" />
					    <a className="toTop-link">
					        <i className="fa fa-long-arrow-up fa-lg toTop-link-icon" aria-hidden="true"></i>
					        <br/>
					        TOP
					    </a>
					    <hr className="toTop-hr" />
					</div>

				</ScrollToTop>
                <div className="footer-sponsors">
                    {this.createSponsors(data.sponsor)}
                    <BeSponsor />
                </div>
                <FooterBottom />
            </div>
        );
    }
}

export default Footer;
