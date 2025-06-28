import { startStimulusApp } from '@symfony/stimulus-bundle';
import RevealController from '@stimulus-components/reveal'

const app = startStimulusApp();

app.debug = true; // process.env.NODE_ENV === 'development'

app.register('reveal', RevealController)
