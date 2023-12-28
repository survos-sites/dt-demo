
import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();
app.debug = false; // process.env.NODE_ENV === 'development'
