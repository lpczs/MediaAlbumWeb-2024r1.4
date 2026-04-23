import React, { LegacyRef, useRef, useState } from 'react';
import { List, ListItem, TextInput } from '@taopix/taopix-design-system';
import axios, { AxiosResponse } from 'axios';
import { useTranslation } from 'react-i18next';
import { useErrorBoundary } from 'react-error-boundary';
import { LiveSearch } from '../../../../types';

declare global {
  interface Window { logOut: () => void, google: any; gMessageLoading: string; gLangCode: string }
}
export interface ProductSearchProps {
  sessionRef: number
  searchResult: Array<any>
  assignment: Array<string>
  id: number
  setSearchResult: Function
  chooseProduct: Function
  label: string
}

export const ProductSearch = ({ searchResult, assignment, id, setSearchResult, chooseProduct,label,...props }: ProductSearchProps) => {

  const {t} = useTranslation();
  const { showBoundary } = useErrorBoundary();

  const [abortController, setAbortController] = useState<AbortController>(undefined);
  
  const liveSearch = (e: any) => {
    fetchData(e.target.value)
  }

  const fetchData = async (liveSearchQuery: string) => {
    try {
      const newSignal = new AbortController();

      if (abortController !== undefined) {
        abortController.abort();
      }

      if (liveSearchQuery !== '') {
        // Create a new CancelToken
        setAbortController(newSignal);

        let formParams = new FormData();
        formParams.append('ref', props.sessionRef.toString());
        formParams.append('fsaction', 'AdminExperienceEditing.liveSearch');
        formParams.append('query', liveSearchQuery);

        axios.post('/api/experience/liveSearch', formParams, {signal: newSignal.signal}
        ).then(function (response: AxiosResponse<LiveSearch>) {
          if ((response.data.hasOwnProperty('success')) && (!response.data.success)) {
            window.logOut();
          } else {
            setSearchResult(response.data.results)
          }
        }).catch((error: any) => {
          //ignore cancel error
          if (!axios.isCancel(error)) {
            showBoundary({
              message: t(error.response.data.error.fullMessage, {ns:'AdminExperience'})
            });
          }
        });
      }
    } catch (error) {
      showBoundary({
        message: t(error.response.data.error.fullMessage, {ns:'AdminExperience'})
      });
    }
  }

  const searchInputeRef: LegacyRef<any> = useRef();

  return (
    <div className="flex mt-5">
      <div className='flex-none p-2 w-1/2 border-2 rounded'>
        <TextInput ref={searchInputeRef} label={label} id={'productSearch_' + id} name={'searchProducts'} onKeyUp={(e) => liveSearch(e)} />
        {
          ((searchResult) && (searchResult.length > 0))
            ?
            <div className="border-2 rounded overflow-auto h-48 g-slate-200">
              <List size={'medium'}>
                {searchResult.map(result => (
                  <ListItem key={result.id} label={result.productName}>
                    <p>{result.productName}</p>
                    <img className="w-full" src={`data:image/png;base64,${result.base64Img}`} onClick={(e) => { searchInputeRef.current.value = ''; chooseProduct(e, '*.*.' + result.collectionCode + '.' + result.productCode)}} />
                  </ListItem>
                ))}
              </List>
            </div>
            :
            <></>
        }
      </div>
      <div className='flex-auto p-2 w-1/2 border-2 rounded'>
        {
          (assignment.length > 0)
            ?
            <div className="overflow-auto h-48">
              <h2>CHOSEN PRODUCTS - FORMAT BRANDCODE.LICENSEKEYCODE.COLLECTIONCODE.LAYOUTCODE WITH * MEANING ALL</h2>
              <List size={'large'}>
                {assignment.map(productKey => (
                  <ListItem key={productKey} label={productKey}>
                    {/*<img src={result.image} alt={result.name}/>*/}
                    <p className='flex-none ml-2'>{productKey}</p>
                  </ListItem>
                ))}
              </List>
            </div>
            :
            <></>
        }
      </div>
    </div>
  )
};

