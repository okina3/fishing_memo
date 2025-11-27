import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// MicroModal ライブラリをインポート。
import MicroModal from 'micromodal';
// MicroModalのモーダル機能が使えるように初期化。
MicroModal.init({
   disableScroll: true
}
);