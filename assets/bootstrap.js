
import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();
app.debug = true; // process.env.NODE_ENV === 'development'
