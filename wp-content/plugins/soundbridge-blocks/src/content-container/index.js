import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './editor';
import save from './save';
import deprecated from './deprecated';
import './editor.scss';
import './style.scss';

registerBlockType(metadata.name, { edit: Edit, save, deprecated });
