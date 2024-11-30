import {CountryFlag} from "react-countryname-flag";

export default function CountryFlagBadge({country, style = { fontSize: '20px' }}) {
    let parsedCountry = country
    if (country === 'US')
        parsedCountry = 'United States of America'

    if (country === 'UK')
        parsedCountry = 'United Kingdom'

    return  <CountryFlag countryName={parsedCountry} style={style} />
}
