/**
 * WireStrap entry point
 */
import { _runNavigateCleanups } from './utils/navigate';

document.addEventListener('livewire:navigating', _runNavigateCleanups);

// Form
import './components/form/input';
import './components/form/select';
import './components/form/autocomplete';
import './components/form/textarea';

// UI
import './components/ui/alert';
import './components/ui/toast';
import './components/ui/accordion';
import './components/ui/slider';
import './components/ui/table';
import './components/ui/counter';
import './components/ui/modal';
import './components/ui/floating-manager';
import './components/ui/tabs';
import './components/ui/menu';

// Magic
import './magic';
