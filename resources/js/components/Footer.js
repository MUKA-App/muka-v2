import '../../sass/footer.scss';
import footerImg from '/images/footer_nobg.png';

export default function Footer () {

    return (
        <div className={"footerContainer"}>
          <img className={"footer-img"} src={footerImg} width="100%"/>
        </div>
    )
}
