<?php

namespace Bannerstop\GlsAde\Api;

use Doctrine\Common\Collections\ArrayCollection;
use Bannerstop\GlsAde\Model\Consignment;
use Bannerstop\GlsAde\Model\ConsignmentDocuments;
use Bannerstop\GlsAde\Model\ConsignmentLabelModes;

/**
 * Class ConsignmentPrepareApi
 * @author Daniel Bojdo <daniel.bojdo@web-it.eu>
 * @see https://ade-test.gls-poland.com/adeplus/pm1/html/webapi/function_list.htm
 * Przygotowywanie paczek/przesyłek (przygotowalnia)
 */
class ConsignmentPrepareApi extends AbstractSessionAwareApi
{
    /**
     * Metoda pozwala na dodanie nowej przesyłki/paczki do tzw. przygotowalni.
     * Metoda nie nadaje paczkom numerów (za wyjątkiem paczek z usługami PS i PR).
     * @see https://ade-test.gls-poland.com/adeplus/pm1/html/webapi/functions/f_prepbox_insert.htm
     *
     * @param Consignment $consignment Dane przesyłki
     * @return int
     */
    public function insertConsignment(Consignment $consignment)
    {
        $response = $this->request('adePreparingBox_Insert', array('consign_prep_data' => $consignment));

        return $response->get('id');
    }

    /**
     * Metoda daje dostęp do identyfikatorów przesyłek znajdujących się w przygotowalni.
     * Zwracanych jest nie więcej niż 100 identyfikatorów uporządkowanych malejąco.
     * Pierwsze wywołanie funkcji powinno się odbyć z parametrem id_start = 0.
     * Zostanie wtedy zwróconych nie więcej niż 100 identyfikatorów uporządkowanych malejąco
     * (tym samym pierwsze wywołanie funkcji daje zawsze najnowsze identyfikatory) mniejszych niż id_start.
     * Jeśli zwróconych zostanie mniej niż 100 identyfikatorów, oznacza to że zostały pobrane wszystkie.
     * Natomiast jeśli zostanie zwróconych 100 identyfikatorów, należy wywołać funkcję przynajmniej jeszcze jeden raz,
     * podając jako parametr id_start wartość ostatniego elementu tablicy identyfikatorów otrzymanej
     * w poprzednim wywołaniu. Kolejne wywołanie będzie konieczne, jeśli liczba identyfikatorów bedzie równa 100.
     * Zatem aby otrzymać pełną listę identyfikatorów należy wywoływać metodę tyle razy, aż na wyjściu pojawi się
     * tablica z mniej niż 100 identyfikatorami.
     * @see https://ade-test.gls-poland.com/adeplus/pm1/html/webapi/functions/f_prepbox_getids.htm
     *
     * @param int $idStart
     * @return ArrayCollection
     */
    public function getConsignmentIds($idStart = 0)
    {
        $response = $this->request('adePreparingBox_GetConsignIDs', array('id_start' => $idStart));

        return $response;
    }

    /**
     * @return ArrayCollection
     */
    public function getAllConsignmentIds()
    {
        $ids = array();
        $idStart = 0;
        while ($result = $this->getConsignmentIds($idStart)) {
            $ids = array_merge($ids, $result->toArray());
            if ($result->count() < 100) {
                break;
            }

            $idStart = end($ids);
            reset($ids);
        }

        return new ArrayCollection($ids);
    }

    /**
     * Metoda daje dostęp do danych przesyłki z przygotowalni.
     * @see https://ade-test.gls-poland.com/adeplus/pm1/html/webapi/functions/f_prepbox_get_consign.htm
     *
     * @param int $id
     * @return Consignment
     */
    public function getConsignment($id)
    {
        /** @var Consignment $response */
        $response = $this->request('adePreparingBox_GetConsign', array('id' => $id));

        if ($response) {
            $response->setId($id);
            $response->setDispatched(false);
        }


        return $response;
    }

    /**
     * Metoda pozwala na usunięcie przesyłki z przygotowalni.
     * Nie można usunąć przesyłek z usługa PS i PR. Nie jest zalecane usuwanie przesyłek,
     * których paczki posiadają nadane numery. Powoduje to utratę numerów z puli numerów Użytkownika
     * i w może doprowadzić do problemów z pracą z systemem.
     * @see https://ade-test.gls-poland.com/adeplus/pm1/html/webapi/functions/f_prepbox_delete_consign.htm
     *
     * @param $id
     * @return int
     */
    public function deleteConsignment($id)
    {
        $response = $this->request('adePreparingBox_DeleteConsign', array('id' => $id));

        return $response->get('id');
    }

    /**
     * Pozwala pobrać z systemu etykiety dla przesyłki znajdującej się w przygotowalni.
     * Przesyłka bez numerów paczek automatycznie je otrzymuje.
     * @see https://ade-test.gls-poland.com/adeplus/pm1/html/webapi/functions/f_prepbox_get_consign_labels.htm
     *
     * @param int $id
     * @param string $mode
     * @return string
     */
    public function getConsignmentLabels($id, $mode = ConsignmentLabelModes::MODE_ONE_LABEL_ON_A4_PDF)
    {
        $response = $this->request('adePreparingBox_GetConsignLabels', array('id' => $id, 'mode' => $mode));

        return base64_decode($response->get('labels'));
    }

    /**
     * Pozwala pobrać z systemu etykiety oraz druk usługi IDENT dla przesyłki znajdującej się w przygotowalni.
     * Przesyłka bez numerów paczek automatycznie je otrzymuje.
     * @see https://ade-test.gls-poland.com/adeplus/pm1/html/webapi/functions/f_prepbox_get_consign_docs.htm
     *
     * @param $id
     * @param string $mode
     * @return ConsignmentDocuments
     */
    public function getConsignmentDocuments($id, $mode = ConsignmentLabelModes::MODE_ONE_LABEL_ON_A4_PDF)
    {
        $response = $this->request('adePreparingBox_GetConsignDocs', array('id' => $id, $mode => $mode));

        return new ConsignmentDocuments(
            $response->get('labels') ? base64_decode($response->get('labels')) : null,
            $response->get('ident') ? base64_decode($response->get('ident')) : null
        );
    }
}
