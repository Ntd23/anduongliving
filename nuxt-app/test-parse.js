import { parseShortcodeAttributes } from './utils/shortcode/core.js';

const shortcode = '[pricing title="Dịch vụ bổ sung" subtitle="Giá tốt nhất" quantity="2" title_1="Dọn phòng" price_1="200,000₫"]';

const attributes = parseShortcodeAttributes(shortcode);
console.log(attributes);