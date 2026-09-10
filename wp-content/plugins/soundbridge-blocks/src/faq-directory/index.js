import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './editor';
import save from './save';
import './editor.scss';
import './style.scss';
import '../shared/pale-blue-highlight';
registerBlockType(metadata.name, { edit: Edit, save });
